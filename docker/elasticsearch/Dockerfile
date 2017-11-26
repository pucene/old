# https://github.com/elastic/elasticsearch-docker
FROM docker.elastic.co/elasticsearch/elasticsearch:6.0.0

RUN rm /usr/share/elasticsearch/config/elasticsearch.yml
ADD elasticsearch.yml /usr/share/elasticsearch/config/

RUN chown -R elasticsearch:elasticsearch /usr/share/elasticsearch/data
RUN chown -R elasticsearch:elasticsearch /usr/share/elasticsearch/config

# Add your elasticsearch plugins setup here
# Example: RUN elasticsearch-plugin install analysis-icu
